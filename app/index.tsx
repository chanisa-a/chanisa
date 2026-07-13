import * as ImagePicker from 'expo-image-picker';
import React, { useMemo, useState } from 'react';
import { Image, Modal, Pressable, SafeAreaView, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';

type Food = { id: number; name: string; category: string; price: number; description: string; imageUri: string; available: boolean };
type Draft = Omit<Food, 'id'>;

const initialFoods: Food[] = [
  { id: 1, name: 'ข้าวกะเพราเนื้อไข่ดาว', category: 'จานหลัก', price: 159, description: 'เนื้อสับผัดกะเพรา พร้อมไข่ดาว', imageUri: '', available: true },
  { id: 2, name: 'ผัดไทยกุ้งสด', category: 'เส้น', price: 169, description: 'เส้นจันท์เหนียวนุ่ม กุ้งสดตัวโต', imageUri: '', available: true },
  { id: 3, name: 'ปีกไก่ทอดน้ำปลา', category: 'ของทานเล่น', price: 129, description: 'ทอดกรอบ หอมกลิ่นน้ำปลา 6 ชิ้น', imageUri: '', available: true },
  { id: 4, name: 'ชาไทยเย็น', category: 'เครื่องดื่ม', price: 65, description: 'ชาไทยเข้มข้น หวานมันกำลังดี', imageUri: '', available: false },
];

const emptyDraft: Draft = { name: '', category: 'จานหลัก', price: 0, description: '', imageUri: '', available: true };
const categories = ['ทั้งหมด', 'จานหลัก', 'เส้น', 'ของทานเล่น', 'เครื่องดื่ม'];

export default function AdminMenuScreen() {
  const [foods, setFoods] = useState(initialFoods);
  const [search, setSearch] = useState('');
  const [filter, setFilter] = useState('ทั้งหมด');
  const [modalVisible, setModalVisible] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [draft, setDraft] = useState<Draft>(emptyDraft);
  const [error, setError] = useState('');

  const visibleFoods = useMemo(() => foods.filter((food: Food) => {
    const keyword = search.trim().toLowerCase();
    return (filter === 'ทั้งหมด' || food.category === filter) &&
      (food.name.toLowerCase().includes(keyword) || food.description.toLowerCase().includes(keyword));
  }), [foods, filter, search]);

  const openCreate = () => {
    setEditingId(null);
    setDraft(emptyDraft);
    setError('');
    setModalVisible(true);
  };

  const openEdit = (food: Food) => {
    const { id, ...foodDraft } = food;
    setEditingId(id);
    setDraft(foodDraft);
    setError('');
    setModalVisible(true);
  };

  const saveFood = () => {
    if (!draft.name.trim() || draft.price <= 0) {
      setError('กรุณากรอกชื่อเมนูและราคาที่ถูกต้อง');
      return;
    }
    if (editingId === null) {
      setFoods((current: Food[]) => [{ ...draft, id: Date.now() }, ...current]);
    } else {
      setFoods((current: Food[]) => current.map((food: Food) => food.id === editingId ? { ...draft, id: editingId } : food));
    }
    setModalVisible(false);
  };

  const removeFood = (id: number) => setFoods((current: Food[]) => current.filter((food: Food) => food.id !== id));
  const toggleAvailable = (id: number) => setFoods((current: Food[]) => current.map((food: Food) => food.id === id ? { ...food, available: !food.available } : food));

  return (
    <SafeAreaView style={styles.screen}>
      <View style={styles.header}>
        <Pressable style={styles.headerIcon}><Text style={styles.menuIcon}>☰</Text></Pressable>
        <View style={styles.headerCenter}>
          <Text style={styles.headerTitle}>เมนูอาหาร</Text>
          <Text style={styles.headerSubtitle}>ADMIN PANEL</Text>
        </View>
        <View style={styles.avatar}><Text style={styles.avatarText}>A</Text></View>
      </View>

      <ScrollView
        style={styles.list}
        contentContainerStyle={styles.pageContent}
        keyboardShouldPersistTaps="handled"
        decelerationRate="normal">
        <View style={styles.toolbar}>
          <View style={styles.searchBox}>
            <Text style={styles.searchIcon}>⌕</Text>
            <TextInput value={search} onChangeText={setSearch} style={styles.searchInput} placeholder="ค้นหาเมนู..." placeholderTextColor="#9AA1B1" />
          </View>
          <Pressable style={styles.addButton} onPress={openCreate}><Text style={styles.addButtonText}>+ เพิ่มเมนู</Text></Pressable>
        </View>

        <ScrollView
          horizontal
          nestedScrollEnabled
          style={styles.filterScroll}
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.filters}>
          {categories.map((category: string) => (
            <Pressable key={category} onPress={() => setFilter(category)} style={[styles.filter, filter === category && styles.filterActive]}>
              <Text style={[styles.filterText, filter === category && styles.filterTextActive]}>{category}</Text>
            </Pressable>
          ))}
        </ScrollView>

        <View style={styles.listContent}>
          <View style={styles.summaryRow}>
            <View><Text style={styles.summaryTitle}>รายการอาหาร</Text><Text style={styles.summarySub}>จัดการเมนู ราคา และสถานะขาย</Text></View>
            <Text style={styles.count}>{visibleFoods.length} เมนู</Text>
          </View>

          {visibleFoods.map((food: Food) => (
            <View key={food.id} style={styles.foodCard}>
            <View style={styles.foodThumb}>
              {food.imageUri ? (
                <Image source={{ uri: food.imageUri }} style={styles.foodImage} resizeMode="cover" />
              ) : (
                <View style={styles.noImage}><Text style={styles.noImageIcon}>▧</Text><Text style={styles.noImageText}>ไม่มีรูป</Text></View>
              )}
            </View>
            <View style={styles.foodCopy}>
              <View style={styles.nameRow}>
                <Text style={styles.foodName}>{food.name}</Text>
                <View style={[styles.status, !food.available && styles.statusOff]}><Text style={[styles.statusText, !food.available && styles.statusTextOff]}>{food.available ? 'พร้อมขาย' : 'ปิดขาย'}</Text></View>
              </View>
              <Text style={styles.categoryText}>{food.category}</Text>
              <Text style={styles.description} numberOfLines={2}>{food.description}</Text>
              <Text style={styles.price}>฿{food.price}</Text>
            </View>
            <View style={styles.actions}>
              <Pressable style={styles.iconAction} onPress={() => openEdit(food)}><Text style={styles.editText}>✎</Text></Pressable>
              <Pressable style={styles.iconAction} onPress={() => toggleAvailable(food.id)}><Text style={styles.powerText}>◉</Text></Pressable>
              <Pressable style={[styles.iconAction, styles.deleteAction]} onPress={() => removeFood(food.id)}><Text style={styles.deleteText}>×</Text></Pressable>
            </View>
            </View>
          ))}

          {visibleFoods.length === 0 && <View style={styles.empty}><Text style={styles.emptyIcon}>🍽️</Text><Text style={styles.emptyTitle}>ไม่พบเมนู</Text><Text style={styles.emptyText}>ลองเปลี่ยนคำค้นหา หรือเพิ่มเมนูใหม่</Text></View>}
        </View>
      </ScrollView>

      <View style={styles.bottomNav}>
        <Pressable style={styles.navItem}><Text style={styles.navIcon}>⌂</Text><Text style={styles.navText}>ภาพรวม</Text></Pressable>
        <Pressable style={styles.navItem}><Text style={[styles.navIcon, styles.navActive]}>▤</Text><Text style={[styles.navText, styles.navActive]}>เมนู</Text><View style={styles.activeLine} /></Pressable>
        <Pressable style={styles.navItem}><Text style={styles.navIcon}>▣</Text><Text style={styles.navText}>ออเดอร์</Text></Pressable>
        <Pressable style={styles.navItem}><Text style={styles.navIcon}>⚙</Text><Text style={styles.navText}>ตั้งค่า</Text></Pressable>
      </View>

      <FoodModal visible={modalVisible} editing={editingId !== null} draft={draft} error={error} onChange={setDraft} onClose={() => setModalVisible(false)} onSave={saveFood} />
    </SafeAreaView>
  );
}

function FoodModal({ visible, editing, draft, error, onChange, onClose, onSave }: { visible: boolean; editing: boolean; draft: Draft; error: string; onChange: (draft: Draft) => void; onClose: () => void; onSave: () => void }) {
  const pickImage = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.8,
    });

    if (!result.canceled) {
      onChange({ ...draft, imageUri: result.assets[0].uri });
    }
  };

  return (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <ScrollView contentContainerStyle={styles.modalScroll} keyboardShouldPersistTaps="handled">
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}><View><Text style={styles.modalEyebrow}>{editing ? 'EDIT MENU' : 'NEW MENU'}</Text><Text style={styles.modalTitle}>{editing ? 'แก้ไขเมนู' : 'เพิ่มเมนูอาหาร'}</Text></View><Pressable onPress={onClose}><Text style={styles.close}>×</Text></Pressable></View>
            <Text style={styles.label}>รูปอาหาร</Text>
            <Pressable style={styles.imagePicker} onPress={pickImage}>
              {draft.imageUri ? (
                <Image source={{ uri: draft.imageUri }} style={styles.imagePreview} resizeMode="cover" />
              ) : (
                <View style={styles.imagePickerEmpty}><Text style={styles.uploadIcon}>↑</Text><Text style={styles.uploadTitle}>เลือกรูปภาพ</Text><Text style={styles.uploadHint}>JPG หรือ PNG · อัตราส่วนแนะนำ 4:3</Text></View>
              )}
              <View style={styles.imagePickerButton}><Text style={styles.imagePickerButtonText}>{draft.imageUri ? 'เปลี่ยนรูป' : 'เลือกไฟล์'}</Text></View>
            </Pressable>
            <Label text="ชื่อเมนู *"><TextInput value={draft.name} onChangeText={(name: string) => onChange({ ...draft, name })} style={styles.input} placeholder="เช่น ข้าวกะเพรา" placeholderTextColor="#9AA1B1" /></Label>
            <View style={styles.formRow}>
              <Label text="ราคา (บาท) *"><TextInput value={draft.price ? String(draft.price) : ''} onChangeText={(value: string) => onChange({ ...draft, price: Number(value.replace(/[^0-9.]/g, '')) || 0 })} style={styles.input} keyboardType="numeric" placeholder="0" placeholderTextColor="#9AA1B1" /></Label>
            </View>
            <Text style={styles.label}>หมวดหมู่</Text>
            <View style={styles.modalCategories}>{categories.slice(1).map((item: string) => <Pressable key={item} onPress={() => onChange({ ...draft, category: item })} style={[styles.modalCategory, draft.category === item && styles.modalCategoryActive]}><Text style={[styles.modalCategoryText, draft.category === item && styles.modalCategoryTextActive]}>{item}</Text></Pressable>)}</View>
            <Label text="รายละเอียด"><TextInput value={draft.description} onChangeText={(description: string) => onChange({ ...draft, description })} style={[styles.input, styles.textarea]} multiline placeholder="รายละเอียดสั้น ๆ ของเมนู" placeholderTextColor="#9AA1B1" /></Label>
            <Pressable style={styles.availableRow} onPress={() => onChange({ ...draft, available: !draft.available })}><View style={[styles.checkbox, draft.available && styles.checkboxActive]}>{draft.available && <Text style={styles.check}>✓</Text>}</View><Text style={styles.availableLabel}>เปิดขายเมนูนี้</Text></Pressable>
            {!!error && <Text style={styles.error}>{error}</Text>}
            <View style={styles.modalActions}><Pressable style={styles.cancelButton} onPress={onClose}><Text style={styles.cancelText}>ยกเลิก</Text></Pressable><Pressable style={styles.saveButton} onPress={onSave}><Text style={styles.saveText}>{editing ? 'บันทึกการแก้ไข' : 'เพิ่มเมนู'}</Text></Pressable></View>
          </View>
        </ScrollView>
      </View>
    </Modal>
  );
}

function Label({ text, children }: { text: string; children: React.ReactNode }) { return <View style={styles.field}><Text style={styles.label}>{text}</Text>{children}</View>; }

const purple = '#8B5CF6';
const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F7F8FB' }, header: { height: 68, backgroundColor: '#FFFFFF', borderBottomWidth: 1, borderBottomColor: '#ECEEF3', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 18 }, headerIcon: { width: 38 }, menuIcon: { color: '#4C5261', fontSize: 18 }, headerCenter: { flex: 1, alignItems: 'center' }, headerTitle: { color: purple, fontSize: 19, fontWeight: '900' }, headerSubtitle: { color: '#A1A6B2', fontSize: 8, letterSpacing: 1.5, marginTop: 2 }, avatar: { width: 31, height: 31, borderRadius: 16, backgroundColor: purple, alignItems: 'center', justifyContent: 'center' }, avatarText: { color: '#FFFFFF', fontSize: 12, fontWeight: '900' },
  toolbar: { backgroundColor: '#FFFFFF', padding: 14, flexDirection: 'row', gap: 9 }, searchBox: { flex: 1, minHeight: 43, backgroundColor: '#F1F3F7', borderRadius: 11, flexDirection: 'row', alignItems: 'center', paddingHorizontal: 12 }, searchIcon: { color: '#7D879A', fontSize: 20, marginRight: 7 }, searchInput: { flex: 1, color: '#202536', fontSize: 13 }, addButton: { minHeight: 43, borderRadius: 11, backgroundColor: purple, paddingHorizontal: 15, alignItems: 'center', justifyContent: 'center' }, addButtonText: { color: '#FFFFFF', fontSize: 12, fontWeight: '900' },
  filterScroll: { flexGrow: 0, flexShrink: 0, height: 58, backgroundColor: '#FFFFFF', borderBottomWidth: 1, borderBottomColor: '#ECEEF3' }, filters: { minHeight: 57, paddingHorizontal: 16, paddingVertical: 10, gap: 8, alignItems: 'center' }, filter: { height: 36, paddingHorizontal: 16, backgroundColor: '#F4F3FA', borderRadius: 18, alignItems: 'center', justifyContent: 'center' }, filterActive: { backgroundColor: '#EEE7FF', borderWidth: 1, borderColor: '#D9CBFF' }, filterText: { color: '#73798A', fontSize: 11, fontWeight: '700', lineHeight: 16 }, filterTextActive: { color: purple },
  list: { flex: 1 }, pageContent: { paddingBottom: 30 }, listContent: { padding: 16, paddingBottom: 70, width: '100%', maxWidth: 900, alignSelf: 'center' }, summaryRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: 14 }, summaryTitle: { color: '#202536', fontSize: 20, fontWeight: '900' }, summarySub: { color: '#858C9C', fontSize: 11, marginTop: 4 }, count: { color: purple, fontSize: 11, fontWeight: '800' },
  foodCard: { backgroundColor: '#FFFFFF', borderWidth: 1, borderColor: '#E9EBF1', borderRadius: 15, padding: 12, marginBottom: 10, flexDirection: 'row', alignItems: 'center', shadowColor: '#344268', shadowOpacity: 0.04, shadowRadius: 10, elevation: 2 }, foodThumb: { width: 88, height: 72, borderRadius: 12, backgroundColor: '#F2F3F7', overflow: 'hidden' }, foodImage: { width: '100%', height: '100%' }, noImage: { flex: 1, alignItems: 'center', justifyContent: 'center' }, noImageIcon: { color: '#A9AFC0', fontSize: 19 }, noImageText: { color: '#9AA1B1', fontSize: 8, marginTop: 3 }, foodCopy: { flex: 1, minWidth: 0, paddingHorizontal: 13 }, nameRow: { flexDirection: 'row', flexWrap: 'wrap', alignItems: 'center', gap: 8 }, foodName: { color: '#202536', fontSize: 14, fontWeight: '900' }, categoryText: { color: purple, fontSize: 10, fontWeight: '800', marginTop: 4 }, description: { color: '#777F91', fontSize: 11, marginTop: 4 }, price: { color: '#202536', fontSize: 15, fontWeight: '900', marginTop: 5 }, status: { backgroundColor: '#E7FAF5', borderRadius: 99, paddingHorizontal: 7, paddingVertical: 3 }, statusOff: { backgroundColor: '#F1F2F4' }, statusText: { color: '#0B9B7A', fontSize: 8, fontWeight: '900' }, statusTextOff: { color: '#858C9C' }, actions: { flexDirection: 'row', gap: 5 }, iconAction: { width: 32, height: 32, borderRadius: 9, backgroundColor: '#F3F0FC', alignItems: 'center', justifyContent: 'center' }, editText: { color: purple, fontSize: 17 }, powerText: { color: '#0FBAA8', fontSize: 15 }, deleteAction: { backgroundColor: '#FFF0F1' }, deleteText: { color: '#E34D59', fontSize: 22 },
  empty: { alignItems: 'center', paddingVertical: 70 }, emptyIcon: { fontSize: 45, opacity: 0.5 }, emptyTitle: { color: '#202536', fontSize: 16, fontWeight: '900', marginTop: 12 }, emptyText: { color: '#858C9C', fontSize: 11, marginTop: 5 },
  bottomNav: { height: 70, backgroundColor: '#151519', flexDirection: 'row', alignItems: 'stretch', paddingHorizontal: 8 }, navItem: { flex: 1, alignItems: 'center', justifyContent: 'center', position: 'relative' }, navIcon: { color: '#85858E', fontSize: 19 }, navText: { color: '#85858E', fontSize: 9, marginTop: 3 }, navActive: { color: '#FFFFFF', fontWeight: '800' }, activeLine: { position: 'absolute', bottom: 0, width: 34, height: 3, backgroundColor: purple, borderRadius: 2 },
  overlay: { flex: 1, backgroundColor: 'rgba(20,21,27,0.55)' }, modalScroll: { flexGrow: 1, justifyContent: 'center', padding: 18 }, modalCard: { width: '100%', maxWidth: 560, alignSelf: 'center', backgroundColor: '#FFFFFF', borderRadius: 20, padding: 22 }, modalHeader: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 10 }, modalEyebrow: { color: purple, fontSize: 9, fontWeight: '900', letterSpacing: 1.5 }, modalTitle: { color: '#202536', fontSize: 22, fontWeight: '900', marginTop: 4 }, close: { color: '#777F91', fontSize: 28 }, imagePicker: { height: 145, borderWidth: 1, borderStyle: 'dashed', borderColor: '#CFC7E7', borderRadius: 12, backgroundColor: '#FAF8FF', overflow: 'hidden', alignItems: 'center', justifyContent: 'center' }, imagePreview: { width: '100%', height: '100%' }, imagePickerEmpty: { alignItems: 'center' }, uploadIcon: { color: purple, fontSize: 24, fontWeight: '800' }, uploadTitle: { color: '#303546', fontSize: 12, fontWeight: '900', marginTop: 3 }, uploadHint: { color: '#8A91A1', fontSize: 9, marginTop: 3 }, imagePickerButton: { position: 'absolute', right: 9, bottom: 9, backgroundColor: purple, borderRadius: 8, paddingHorizontal: 11, paddingVertical: 7 }, imagePickerButtonText: { color: '#FFFFFF', fontSize: 10, fontWeight: '900' }, field: { flex: 1, minWidth: 120, marginTop: 14 }, label: { color: '#303546', fontSize: 11, fontWeight: '800', marginBottom: 7 }, input: { minHeight: 46, borderWidth: 1, borderColor: '#DFE2EA', borderRadius: 10, paddingHorizontal: 12, color: '#202536', fontSize: 13 }, formRow: { flexDirection: 'row', gap: 12 }, textarea: { minHeight: 78, paddingTop: 12, textAlignVertical: 'top' }, modalCategories: { flexDirection: 'row', flexWrap: 'wrap', gap: 7 }, modalCategory: { paddingHorizontal: 11, paddingVertical: 8, borderRadius: 8, backgroundColor: '#F2F3F6' }, modalCategoryActive: { backgroundColor: '#EEE7FF' }, modalCategoryText: { color: '#777F91', fontSize: 10, fontWeight: '700' }, modalCategoryTextActive: { color: purple }, availableRow: { flexDirection: 'row', alignItems: 'center', marginTop: 17 }, checkbox: { width: 20, height: 20, borderRadius: 6, borderWidth: 1, borderColor: '#CBD0DB', alignItems: 'center', justifyContent: 'center' }, checkboxActive: { backgroundColor: purple, borderColor: purple }, check: { color: '#FFFFFF', fontSize: 12, fontWeight: '900' }, availableLabel: { color: '#303546', fontSize: 12, fontWeight: '700', marginLeft: 8 }, error: { color: '#D74753', fontSize: 11, marginTop: 12 }, modalActions: { flexDirection: 'row', justifyContent: 'flex-end', gap: 9, marginTop: 22 }, cancelButton: { minHeight: 43, borderRadius: 10, borderWidth: 1, borderColor: '#DFE2EA', paddingHorizontal: 17, alignItems: 'center', justifyContent: 'center' }, cancelText: { color: '#606778', fontSize: 12, fontWeight: '800' }, saveButton: { minHeight: 43, borderRadius: 10, backgroundColor: purple, paddingHorizontal: 18, alignItems: 'center', justifyContent: 'center' }, saveText: { color: '#FFFFFF', fontSize: 12, fontWeight: '900' },
});
